function addRow() {
    var div = document.createElement('div');
  
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

const getTasksFromServer = (user_id) => {
    var authenticationHeader = new Headers({
        'Key': localStorage.getItem("Key")
    })
    var contentRequest = new Request(`http://localhost/circl/api/v1/users/${user_id}/tasks`, {
        method: "GET",
        headers: authenticationHeader
    })

    fetch(contentRequest)
    .then((response) => {
        if(response.status == 200) {
            return response.json()
        } else if (response.status == 404) {
            return Promise.reject()
        }
    })
    .then((tasks) => {
        tasks = tasks['tasks']
        console.log(tasks)
        tasks.forEach((task) => {
            let div = document.createElement("div");
            div.classList.add("box")

            var taskName = task['name']
            var taskSubject = task['subject']
            var taskImportance = task['importance'] > 0 ? "!".repeat(task['importance']) : ""
            var taskDescription = task['description']

            div.innerHTML = `
                <h1>${taskName}</h1>
                <h2>${taskSubject} ${taskImportance}</h2>
                <p>${taskDescription}</p>
            `

            document.querySelector("#tasks").appendChild(div)
        })
    })
}

export const onLoad = () => {
    getTasksFromServer(localStorage.getItem("ID"))
}