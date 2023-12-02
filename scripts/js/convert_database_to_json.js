const fs = require('fs');
const path = require('path');

function convertToJSON(input) {
    const lines = input.split('\n');
    const jsonResult = [];
    let currentObject = {};

    lines.forEach(line => {
        if (line.includes('*****')) {
            if (Object.keys(currentObject).length > 0) {
                jsonResult.push(currentObject);
                currentObject = {};
            }
        } else {
            const [key, value] = line.split(':').map(s => s.trim());
            if (key) currentObject[key] = value ? String(value) : null;
        }
    });

    if (Object.keys(currentObject).length > 0) {
        jsonResult.push(currentObject);
    }

    return JSON.stringify(jsonResult, null, 2);
}

// Check if an input file was provided
if (process.argv.length < 3) {
    console.error('Please provide the path to the input file.');
    process.exit(1);
}

const inputFile = process.argv[2];
const outputPath = path.join(path.dirname(inputFile), path.basename(inputFile, path.extname(inputFile)) + '.json');

fs.readFile(inputFile, 'utf8', (err, data) => {
    if (err) {
        console.error('Error reading the file:', err);
        return;
    }

    const jsonOutput = convertToJSON(data);

    fs.writeFile(outputPath, jsonOutput, 'utf8', (err) => {
        if (err) {
            console.error('Error writing the JSON file:', err);
        } else {
            console.log('JSON file has been saved:', outputPath);
        }
    });
});
