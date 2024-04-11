import { statSync } from 'fs';
import { readdir, readFile } from 'fs/promises';
import { rename as moveFile, writeFile } from 'fs/promises';
import { join as joinPath } from 'path';
import process from 'process';

const repository = JSON.parse( process.argv[2] );

const skip_dirs = [
	'.github',
	'.git'
];

/**
 * @param {string} dirPath 
 * @param {(filePath: string) => Promise<void>} callback
 */
const traverseDirectory = async ( dirPath, callback ) => {
	if ( skip_dirs.includes( dirPath ) ) {
		console.log( 'Skipping %s', dirPath );
		return;
	}
	console.log( 'Traversing %s', dirPath );

	// Read the contents of the directory
	const files = await readdir( dirPath );
	
	// Loop over each file in that directory
	for ( const file of files ) {
		// Construct the full path to the file
		const filePath = joinPath( dirPath, file );

		// Check if the current entry is a file
		if ( statSync( filePath ).isFile() ) {
			await callback( filePath );
		} else {
			// Recursively traverse directories
			await traverseDirectory( filePath, callback );
		}
	}
};

/**
 * Build a template using envs
 * @param {string} filePath 
 */
const buildTemplate = async ( filePath ) => {
	console.log( 'Building %s', filePath );

	// Read the template file
	const templateFile = await readFile( filePath, 'utf-8' );

	// Do all the swaps!
	let renderedTemplate = templateFile;

	// Some README.md specific strings...
	if ( 'README.md' == filePath ) {
		renderedTemplate = renderedTemplate.replaceAll(
			'EXAMPLE_REPO_NAME',
			repository.custom_properties['human-title'] ?? repository.name
		);
		renderedTemplate = renderedTemplate.replaceAll(
			'EXAMPLE_REPO_DESCRIPTION',
			repository.description
		);
		renderedTemplate = renderedTemplate.replaceAll(
			'EXAMPLE_REPO_PROD_URL',
			repository.custom_properties['site-production-url']
		);
		renderedTemplate = renderedTemplate.replaceAll(
			'EXAMPLE_REPO_DEV_URL',
			repository.custom_properties['site-development-url']
		);
	}

	if ( 'project' == repository.custom_properties['repo-type'] ) {
		// Some existing string swaps...
		renderedTemplate = renderedTemplate.replaceAll(
			'A demo project for showcasing standardized build processes for various asset types.',
			repository.description
		);
		renderedTemplate = renderedTemplate.replaceAll(
			'build-processes-demo-production.mystagingwebsite.com',
			repository.custom_properties['site-production-url']
		);
		renderedTemplate = renderedTemplate.replaceAll(
			'build-processes-demo',
			repository.name
		);
		renderedTemplate = renderedTemplate.replaceAll(
			'build_processes_demo',
			repository.custom_properties['php-globals-long-prefix']
		);
		renderedTemplate = renderedTemplate.replaceAll(
			'bpd',
			repository.custom_properties['php-globals-short-prefix']
		);
		renderedTemplate = renderedTemplate.replaceAll(
			'BPD',
			repository.custom_properties['php-globals-short-prefix'].toUpperCase()
		);
	} else if ( 'plugin' == repository.custom_properties['repo-type'] ) {
		renderedTemplate = renderedTemplate.replaceAll(
			'Team51 Plugin Scaffold',
			repository.custom_properties['human-title'] ?? repository.name
		);
		renderedTemplate = renderedTemplate.replaceAll(
			'A scaffold for WP.com Special Projects plugins.',
			repository.description
		);
		renderedTemplate = renderedTemplate.replaceAll(
			'team51-plugin-scaffold',
			repository.name
		);
		renderedTemplate = renderedTemplate.replaceAll(
			'wpcomsp-scaffold',
			repository.name
		);
		renderedTemplate = renderedTemplate.replaceAll(
			'WPCOMSpecialProjects\\Scaffold',
			'WPCOMSpecialProjects\\' + repository.name
		);
		renderedTemplate = renderedTemplate.replaceAll(
			'WPCOMSpecialProjects\\\\Scaffold',
			'WPCOMSpecialProjects\\\\' + repository.name
		);
		renderedTemplate = renderedTemplate.replaceAll(
			'wpcomsp_scaffold',
			'wpcomsp_' + repository.custom_properties['php-globals-short-prefix']
		);
		renderedTemplate = renderedTemplate.replaceAll(
			'WPCOMSP_SCAFFOLD',
			'WPCOMSP_' + repository.custom_properties['php-globals-short-prefix'].toUpperCase
		);

	}

	// If stuff has changed, say so and write it back out.  If there were no changes, do nothing.
	if ( renderedTemplate !== templateFile ) {
		console.log( "	Changes were made.  Writing file." );
		// Write back over the templated file
		await writeFile( filePath, renderedTemplate );
	}

};

await traverseDirectory( '.', buildTemplate );
