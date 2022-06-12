require(['codemirror'], function (CodeMirror) {
    var readFileTextArea = document.querySelector('.codemirror-textarea');
    var writeFileTextArea = document.querySelector('.codemirror-textarea-edit');
    if (readFileTextArea){
        var showMode = CodeMirror.fromTextArea(readFileTextArea, {
            lineNumbers: true,
            mode: "application/xml",
            theme:'material-palenight',
            readOnly: 'nocursor',
            matchBrackets: true
        });
        showMode.setSize(null, 600);
    }

    if (writeFileTextArea){
        var editMode = CodeMirror.fromTextArea(writeFileTextArea, {
            lineNumbers: true,
            mode: "application/xml",
            theme:'material-palenight',
            matchBrackets: true
        });
        editMode.setSize(null, 600);
    }

});