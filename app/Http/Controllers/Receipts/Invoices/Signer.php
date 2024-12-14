<?php

namespace App\Http\Controllers\Receipts\Invoices;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class Signer
{
    private string $openssl;

    private string $issuer;

    /**
     * Directory where all the files will be created temporaly
     */
    private string $short_path;

    /**
     * Directory where all the files will be created temporaly
     */
    private string $path;

    private $serial = null;

    private $invoice_hash = null;

    private $certificate_hash = null;

    private $certificate_b64 = null;

    private $modulus_b64 = null;

    private $exponent_b64 = null;

    private $password = null;

    private $generatedFiles = [
        'certificate_hash.txt',
        'certificate.der',
        'certificate.p12',
        'certificate.pem',
        'certificate.sha1',
        'invoice_for_hash.xml',
        'invoice_for_hash.txt',
        'invoice_hash.sha1',
        'invoice_hash.txt',
        'invoice.xml',
        'modulus_exponent.txt',
        'private_key.pem',
        'signature.sha1',
        'signature.txt',
        'SignedInfo.txt'
    ];

    public function sign(string $raw, User $user, string $openssl) // : string
    {
        $this->short_path = '/certificates' . "/$user->id.d";
        $this->path = config('filesystems.disks.local.root') . $this->short_path;
        $this->openssl = $openssl;
        $this->password = $user->certificate->password;
        $this->issuer = 
            'CN=AUTORIDAD DE CERTIFICACION SUBCA-2 SECURITY DATA,OU=ENTIDAD DE CERTIFICACION DE INFORMACION,O=SECURITY DATA S.A. 2,C=EC';
        // $user->certificate->owner;

        $certificate = Storage::disk('local')->get('certificates/' . $user->id);
        Storage::disk('local')->put($this->short_path . '/certificate.p12', $certificate);

        $this->extractFiles($this->password);

        $this->extractData();

        $XAdES_BES = $this->constructSign();

        $signed_invoice = str_replace('</factura>', $XAdES_BES, $raw);

        foreach($this->generatedFiles as $generatedFile){
            Storage::disk('local')->delete($this->short_path . '/' . $generatedFile);
        }

        return $signed_invoice;
    }

    private function extractFiles(string $password): void
    {
        // Extract Private Key (private_key.pem)
        $this->execute(
            "$this->openssl pkcs12 -in $this->path/certificate.p12 "
            . "-nocerts -out $this->path/private_key.pem "
            . "-password pass:$password -passout pass:$password"
        );

        // Extract Certificate x509 (certificate.pem)
        $this->execute(
            "$this->openssl pkcs12 -in $this->path/certificate.p12 "
            . "-out $this->path/certificate.pem "
            . "-password pass:$password -passout pass:$password"
        );

        // Get SHA1 hash from .der (certificate_hash.txt)
        $this->execute(
            "$this->openssl x509 -in $this->path/certificate.pem "
            . "-outform DER -out $this->path/certificate.der"
        );
        $this->execute(
            "$this->openssl sha1 -out $this->path/certificate.sha1 "
            . "-binary $this->path/certificate.der"
        );
        $this->execute(
            "$this->openssl base64 -e -in $this->path/certificate.sha1 "
            . "-out $this->path/certificate_hash.txt"
        );
        // Extract modulus and exponent (modulus_exponent.txt)
        $this->execute(
            "$this->openssl rsa -in $this->path/certificate.pem "
            . "-out $this->path/modulus_exponent.txt -text -passin pass:$password"
        );
    }

    private function extractData(): void
    {
        // Extract Serial
        $output = $this->execute(
            "$this->openssl x509 -noout -serial -in $this->path/certificate.pem"
        )['output'][0]; // "serial=272E6866"
        $output = str_replace("serial=", '', $output); // Hexadecimal "272E6866"
        $this->serial = intval($output, 16);

        // Obtain SHA1 hash from invoice
        $invoice = Storage::disk('local')->get($this->short_path . '/invoice.xml');
        $invoice_for_hash = str_replace("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n", '', $invoice);
        Storage::disk('local')->put($this->short_path . '/invoice_for_hash.xml', $invoice_for_hash);
        $this->execute(
            "$this->openssl sha1 -out $this->path/invoice_hash.sha1 "
            . "-binary $this->path/invoice_for_hash.xml"
        );
        $this->execute(
            "$this->openssl base64 -e -in $this->path/invoice_hash.sha1 "
            . "-out $this->path/invoice_hash.txt"
        );
        $this->invoice_hash = str_replace(
            "\n", '', Storage::disk('local')->get($this->short_path . '/invoice_hash.txt')
        );

        // Obtain SHA1 hash from Certificate X509
        $this->certificate_hash = str_replace(
            "\n", '', Storage::disk('local')->get($this->short_path . '/certificate_hash.txt')
        );

        // Obtain Certificate X509 in Base64
        $certificate = Storage::disk('local')->get($this->short_path . '/certificate.pem');
        $start_index = strpos($certificate, "-----BEGIN CERTIFICATE-----\n") + 28;
        $end_index = strpos($certificate, "\n-----END CERTIFICATE-----");
        $certificate = substr($certificate, $start_index, $end_index - $start_index);
        $certificate = str_replace("\n", '', $certificate);
        $this->certificate_b64 = $this->addEndLine($certificate);

        // Extract Modulus
        $modulus_exponent = Storage::disk('local')->get($this->short_path . '/modulus_exponent.txt');
        $start_index = strpos($modulus_exponent, 'modulus:') + 16;
        $end_index = strpos($modulus_exponent, "\npublicExponent");
        $modulus = substr($modulus_exponent, $start_index, $end_index - $start_index);
        $modulus = str_replace("\n", '', $modulus);
        $modulus = str_replace(":", '', $modulus);
        $modulus = str_replace("    ", '', $modulus);
        $modulus_bin = hex2bin($modulus);
        $modulus_b64 = base64_encode($modulus_bin);
        $this->modulus_b64 = $this->addEndLine($modulus_b64);

        // Extract Exponent
        $modulus_exponent = Storage::disk('local')->get($this->short_path . '/modulus_exponent.txt');
        $start_index = strpos($modulus_exponent, '(0x') + 3;
        $end_index = strpos($modulus_exponent, ")\nprivateExponent:");
        $exponent = substr($modulus_exponent, $start_index, $end_index - $start_index);
        // Fix with '0' if string's length isn't even
        $exponent = (mb_strlen($exponent) % 2) > 0 ? '0' . $exponent : $exponent;
        $exponent = hex2bin($exponent);
        $this->exponent_b64 = base64_encode($exponent);
    }

    private function constructSign(): string
    {
        // Generate random numbers
        $numbers = $this->generateRandomNumbers();
        $xmlns = 'xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:etsi="http://uri.etsi.org/01903/v1.3.2#"';

        // Start Sign Construction
        $SignedProperties = '';

        $SignedProperties .= '<etsi:SignedProperties Id="Signature' . $numbers['Signature'] . '-SignedProperties' . $numbers['SignedProperties'] . '">';  //SignedProperties
            $SignedProperties .= '<etsi:SignedSignatureProperties>';
                $SignedProperties .= '<etsi:SigningTime>';

                    $SignedProperties .= date(DATE_ATOM); // '2016-12-24T13:46:43-05:00'
                    // $SignedProperties .= '2016-12-24T13:46:44-05:00';

                $SignedProperties .= '</etsi:SigningTime>';
                $SignedProperties .= '<etsi:SigningCertificate>';
                    $SignedProperties .= '<etsi:Cert>';
                        $SignedProperties .= '<etsi:CertDigest>';
                            $SignedProperties .= '<ds:DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1">';
                            $SignedProperties .= '</ds:DigestMethod>';
                            $SignedProperties .= '<ds:DigestValue>';

                                $SignedProperties .= $this->certificate_hash;

                            $SignedProperties .= '</ds:DigestValue>';
                            $SignedProperties .= '</etsi:CertDigest>';
                            $SignedProperties .= '<etsi:IssuerSerial>';
                                $SignedProperties .= '<ds:X509IssuerName>';

                                    $SignedProperties .= $this->issuer;

                                $SignedProperties .= '</ds:X509IssuerName>';
                            $SignedProperties .= '<ds:X509SerialNumber>';

                                $SignedProperties .= $this->serial;

                            $SignedProperties .= '</ds:X509SerialNumber>';
                            $SignedProperties .= '</etsi:IssuerSerial>';
                        $SignedProperties .= '</etsi:Cert>';
                    $SignedProperties .= '</etsi:SigningCertificate>';
                $SignedProperties .= '</etsi:SignedSignatureProperties>';
                $SignedProperties .= '<etsi:SignedDataObjectProperties>';
                    $SignedProperties .= '<etsi:DataObjectFormat ObjectReference="#Reference-ID-' . $numbers['Reference_ID'] . '">';
                        $SignedProperties .= '<etsi:Description>';

                            $SignedProperties .= 'contenido comprobante';                        

                        $SignedProperties .= '</etsi:Description>';
                        $SignedProperties .= '<etsi:MimeType>';
                            $SignedProperties .= 'text/xml';
                        $SignedProperties .= '</etsi:MimeType>';
                    $SignedProperties .= '</etsi:DataObjectFormat>';
                $SignedProperties .= '</etsi:SignedDataObjectProperties>';
        $SignedProperties .= '</etsi:SignedProperties>';

        // End of SignedProperties

        $SignedProperties_hash = $this->sha1Hash(
            str_replace(
                '<etsi:SignedProperties',
                '<etsi:SignedProperties ' . $xmlns,
                $SignedProperties
            ), 'xml'
        );

        // Start Key Info
        $KeyInfo = '';

        $KeyInfo .= '<ds:KeyInfo Id="Certificate' . $numbers['Certificate'] . '">';
            $KeyInfo .= "\n<ds:X509Data>";
                $KeyInfo .= "\n<ds:X509Certificate>\n";

                    // Certificate X509 in Base64
                    $KeyInfo .= $this->certificate_b64;

                $KeyInfo .= "</ds:X509Certificate>";
                $KeyInfo .= "\n</ds:X509Data>";
                $KeyInfo .= "\n<ds:KeyValue>";
                    $KeyInfo .= "\n<ds:RSAKeyValue>";
                        $KeyInfo .= "\n<ds:Modulus>\n";
                        
                            // Modulus
                            $KeyInfo .= $this->modulus_b64;

                        $KeyInfo .= "</ds:Modulus>";
                        $KeyInfo .= "\n<ds:Exponent>";

                            //$KeyInfo .= 'AQAB';
                            $KeyInfo .= $this->exponent_b64;

                        $KeyInfo .= "</ds:Exponent>";
                    $KeyInfo .= "\n</ds:RSAKeyValue>";
                $KeyInfo .= "\n</ds:KeyValue>";
        $KeyInfo .= "\n</ds:KeyInfo>";

        // End of KeyInfo

        $KeyInfo_hash = $this->sha1Hash(
            str_replace(
                '<ds:KeyInfo',
                '<ds:KeyInfo ' . $xmlns,
                $KeyInfo
            ), 'xml'
        );

        // Start SignedInfo
        $SignedInfo = '';

        $SignedInfo .= '<ds:SignedInfo Id="Signature-SignedInfo' . $numbers['SignedInfo'] . '">';
            $SignedInfo .= "\n<ds:CanonicalizationMethod Algorithm=\"http://www.w3.org/TR/2001/REC-xml-c14n-20010315\">";
            $SignedInfo .= '</ds:CanonicalizationMethod>';
            $SignedInfo .= "\n<ds:SignatureMethod Algorithm=\"http://www.w3.org/2000/09/xmldsig#rsa-sha1\">";
            $SignedInfo .= '</ds:SignatureMethod>';
            $SignedInfo .= "\n<ds:Reference Id=\"SignedPropertiesID" . $numbers['SignedPropertiesID'] . "\" Type=\"http://uri.etsi.org/01903#SignedProperties\" URI=\"#Signature" . $numbers['Signature'] . '-SignedProperties' . $numbers['SignedProperties'] . "\">";
                $SignedInfo .= "\n<ds:DigestMethod Algorithm=\"http://www.w3.org/2000/09/xmldsig#sha1\">";
                $SignedInfo .= '</ds:DigestMethod>';
                $SignedInfo .= "\n<ds:DigestValue>";

                    // Hash from SignedProperties Element
                    $SignedInfo .= $SignedProperties_hash;

                $SignedInfo .= '</ds:DigestValue>';
                $SignedInfo .= "\n</ds:Reference>";
                $SignedInfo .= "\n<ds:Reference URI=\"#Certificate" . $numbers['Certificate'] . '">';
                    $SignedInfo .= "\n<ds:DigestMethod Algorithm=\"http://www.w3.org/2000/09/xmldsig#sha1\">";
                    $SignedInfo .= '</ds:DigestMethod>';
                    $SignedInfo .= "\n<ds:DigestValue>";

                        // Hash from KeyInfo Element
                        $SignedInfo .= $KeyInfo_hash;

                    $SignedInfo .= '</ds:DigestValue>';
                $SignedInfo .= "\n</ds:Reference>";
                $SignedInfo .= "\n<ds:Reference Id=\"Reference-ID-" . $numbers['Reference_ID'] . '" URI="#comprobante">';
                    $SignedInfo .= "\n<ds:Transforms>";
                        $SignedInfo .= "\n<ds:Transform Algorithm=\"http://www.w3.org/2000/09/xmldsig#enveloped-signature\">";
                        $SignedInfo .= '</ds:Transform>';
                    $SignedInfo .= "\n</ds:Transforms>";
                    $SignedInfo .= "\n<ds:DigestMethod Algorithm=\"http://www.w3.org/2000/09/xmldsig#sha1\">";
                    $SignedInfo .= '</ds:DigestMethod>';
                    $SignedInfo .= "\n<ds:DigestValue>";

                        // Invoice Hash
                        $SignedInfo .= $this->invoice_hash;

                    $SignedInfo .= '</ds:DigestValue>';
                $SignedInfo .= "\n</ds:Reference>";
        $SignedInfo .= "\n</ds:SignedInfo>";

        // End of SignedInfo

        $signature = $this->generateSignature(str_replace(
            '<ds:SignedInfo',
            '<ds:SignedInfo ' . $xmlns,
            $SignedInfo
        ));
        $signature = $this->addEndLine(str_replace("\n", '', $signature));

        // Start XAdES-BES
        $xades_bes = '';
        $xades_bes .= '<ds:Signature ' . $xmlns . ' Id="Signature' . $numbers['Signature'] . '">';
            $xades_bes .= "\n" . $SignedInfo;

            $xades_bes .= "\n<ds:SignatureValue Id=\"SignatureValue" . $numbers['SignatureValue'] . "\">\n";

                // Signature Value (encrypted with Private Key)
                $xades_bes .= $signature;

            $xades_bes .= '</ds:SignatureValue>';

            $xades_bes .= "\n" . $KeyInfo;

            $xades_bes .= "\n<ds:Object Id=\"Signature" . $numbers['Signature'] . '-Object' . $numbers['Object'] . '">';
                $xades_bes .= '<etsi:QualifyingProperties Target="#Signature' . $numbers['Signature'] . '">';

                    $xades_bes .= $SignedProperties;

                $xades_bes .= '</etsi:QualifyingProperties>';
            $xades_bes .= '</ds:Object>';
        $xades_bes .= '</ds:Signature></factura>';

        // End of XAdES-BES

        return $xades_bes;
    }

    private function execute($command): null|array
    {
        $result_code = 0;
        $output = [];
        exec($command, $output, $result_code);
        return [
            'output' => $output,
            'result_code' => $result_code
        ];
    }

    private function generateRandomNumbers(): array
    {
        return [
            // In hash
            'Certificate' => /*fake()->numberBetween(990, 999990),*/1666089,
            'Signature' => /*fake()->numberBetween(990, 999990),*/833293,
            'SignedProperties' => /*fake()->numberBetween(990, 999990),*/269335,
            // Out hash
            'SignedInfo' => /*fake()->numberBetween(990, 999990),*/288469,
            'SignedPropertiesID' => /*fake()->numberBetween(990, 999990),*/277260,
            'Reference_ID' => /*fake()->numberBetween(990, 999990),*/723993,
            'SignatureValue' => /*fake()->numberBetween(990, 999990),*/977493,
            'Object' =>  /*fake()->numberBetween(990, 999990)*/597308
        ];
    }

    private function sha1Hash(string $string, string $extension): string
    {
        Storage::disk('local')->put($this->short_path . "/file_for_hash.$extension", $string);

        $this->execute(
            "$this->openssl sha1 -out $this->path/file_for_hash.sha1 "
            . "-binary $this->path/file_for_hash.$extension"
        ); // clean file_for_hash.$extension ; file_for_hash.sha1
        $this->execute(
            "$this->openssl base64 -e -in $this->path/file_for_hash.sha1 "
            . "-out $this->path/file_hash.txt"
        ); // clean file_hash.txt
        $hash = str_replace(
            "\n", '',
            Storage::disk('local')->get($this->short_path . '/file_hash.txt')
        );
        Storage::disk('local')->delete([
            $this->short_path . "/file_for_hash.$extension",
            $this->short_path . '/file_for_hash.sha1',
            $this->short_path . '/file_hash.txt'
        ]);
        return $hash;
    }

    private function addEndLine(string $text): string
    {
        $count = 0;
        do {
            $index = 76 + (77 * $count);
            $text = substr($text, 0, $index) . "\n" . substr($text, $index);
            $count += 1;
        } while($index < mb_strlen($text));
        return $text;
    }

    private function generateSignature(string $SignedInfo): string
    {
        Storage::disk('local')->put($this->short_path . '/SignedInfo.txt', $SignedInfo);

        // Generate SHA1 signature file
        $this->execute(
            "$this->openssl dgst -sha1 -sign $this->path/private_key.pem -passin pass:$this->password -out $this->path/signature.sha1 $this->path/SignedInfo.txt"
        );

        // Generate base64 signature file
        $this->execute(
            "$this->openssl base64 -in $this->path/signature.sha1 -out $this->path/signature.txt"
        );

        return Storage::disk('local')->get($this->short_path . '/signature.txt');
    }
}