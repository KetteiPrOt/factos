import { get, writable } from "svelte/store";

import { persistent } from "./localStorage";

export const url_api = persistent("url_api", "");
export const api_rest = persistent("api_rest", "off")

