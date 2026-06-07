import { createRequire } from 'module';
const require = createRequire(import.meta.url);
const fs = require('fs');

// Try to use pdf-parse
try {
    const pdf = require('pdf-parse');
    const dataBuffer = fs.readFileSync('C:\\Users\\nicho\\Documents\\Kuliah\\Semester 4\\Web Design & Development\\WebDev Week 10.1.pdf');
    const data = await pdf(dataBuffer);
    console.log(data.text);
} catch (e) {
    console.log('pdf-parse not found, installing...');
    const { execSync } = require('child_process');
    execSync('npm install pdf-parse --no-save', { stdio: 'inherit' });
    const pdf = require('pdf-parse');
    const dataBuffer = fs.readFileSync('C:\\Users\\nicho\\Documents\\Kuliah\\Semester 4\\Web Design & Development\\WebDev Week 10.1.pdf');
    const data = await pdf(dataBuffer);
    console.log(data.text);
}
