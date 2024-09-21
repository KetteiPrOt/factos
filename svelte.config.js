import adapter from '@sveltejs/adapter-static';
import { vitePreprocess } from '@sveltejs/vite-plugin-svelte';

const dev = process.env.NODE_ENV === 'development';

/** @type {import('@sveltejs/kit').Config} */
const config = {
	preprocess: vitePreprocess(),
	
	kit: {
		adapter: adapter({
			fallback: '200.html', // archivo que recibe y enruta todas las peticiones del frontend
			pages: 'public' // carpeta donde se coloca la Build de 'npm run build'
		}),
		env: {
			dir: 'resources/frontend/'
		},
		paths: {
			base: dev ? "/app" : ""
		},
		files: {
			assets: 'resources/frontend/static',
			hooks: {
				client: 'resources/frontend/src/hooks.client',
				server: 'resources/frontend/src/hooks.server',
				universal: 'resources/frontend/src/hooks',
			},
			lib: 'resources/frontend/src/lib',
			params: 'resources/frontend/src/params',
			routes: 'resources/frontend/src/routes',
			serviceWorker: 'resources/frontend/src/service-worker',
			appTemplate: 'resources/frontend/src/app.html',
			errorTemplate: 'resources/frontend/src/error.html',
		},
		outDir: '.svelte-kit',
	}
};

export default config;
