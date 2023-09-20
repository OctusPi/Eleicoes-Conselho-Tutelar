import { defineConfig } from 'vite';
import path from 'path';

export default defineConfig({
	base: "/eleicoesct/build",
	build: {
		manifest: true,
		sourcemap: true,
		rollupOptions: {
			input: ['resources/js/app.js', 'resources/css/app.css']
		},
		outDir: 'build',
	},
	resolve: {
		alias: {
			'~b' : path.resolve(__dirname, 'node_modules/bootstrap/dist'),
			'~bi': path.resolve(__dirname, 'node_modules/bootstrap-icons/font'),
		}
	}
});