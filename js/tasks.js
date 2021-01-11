function addRow() {
    const div = document.createElement('div');
  
    div.className = 'box';
  
    div.innerHTML = `
        <p>Test</p>
    `;
  
    document.getElementById('tasks').appendChild(div);
}

function removeRow(elementId) {
    var element = document.getElementById(elementId);
    element.parentNode.removeChild(element);
}