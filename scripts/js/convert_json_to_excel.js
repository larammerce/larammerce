const fs = require('fs');
const json2xls = require('json2xls');

// Read command-line arguments
const inputFile = process.argv[2];
const outputFile = process.argv[3];

if (!inputFile || !outputFile) {
    console.error('Please provide input and output file paths.');
    process.exit(1);
}

// Load the JSON file
fs.readFile(inputFile, 'utf-8', (err, data) => {
    if (err) {
        console.error('Error reading input file:', err);
        process.exit(1);
    }

    // Parse the JSON data
    const jsonData = JSON.parse(data);

    // Convert the JSON data to XLS format
    const xlsData = json2xls(jsonData);

    // Write the XLS data to a new Excel file
    fs.writeFileSync(outputFile, xlsData, 'binary');
});
