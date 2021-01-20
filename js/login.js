const main = () => {
    document.querySelectorAll(".field-wrapper .field-placeholder").forEach((e)=>{
        e.addEventListener("click", () => {
            e.parentElement.children[0].focus()
        })
    })

    document.querySelectorAll(".field-wrapper input").forEach((i)=>{
        i.addEventListener("keyup", () => {
            var p = i.parentElement.classList
            if(i.value.trim()) {
                p.add("hasValue");
            } else {
                p.remove("hasValue");
            }
        })
    })
    

    document.querySelector("button.submit").addEventListener("click", (e) => {
        e.preventDefault() 

        input = document.querySelectorAll("input.input")

        let body = JSON.stringify({
            email: input[0].value,
            password: input[1].value,
            secret: "cocaine"
        })

        const authenticationRequest = new Request('http://localhost/circl/api/v1/auth', {
            method: 'POST',
            mode: 'no-cors',
            body: body,
        })

        fetch(authenticationRequest)
        .then((response) => {
            if(response.status == 201) {
                return response.json()      
            } else if (response.status == 401){
                //warnUserAboutWrongCredentials()
                return Promise.reject("wrongCredentials")
            } else {
                //warnUserAboutServerError()
                console.error("Something went wrong on our part, sorry for the inconvenience")
                console.log(response.json())
                return Promise.reject("error500")
            }
        })
        .then((request) => {
            console.log("I AM IN!")
            user_id = request['user_id']
            key = request['key']
            localStorage.setItem("Key", key)
            localStorage.setItem("ID", user_id)
            window.location.assign("http://localhost/circl/dashboard.html")
        })
        .catch(err => console.log(err))
    })
}

const loadMainPage = () => {
    const pageRequest = new Request('http://localhost/circl/dashboard.html', {
        method: 'GET'
    })

    fetch(pageRequest)
    .then((response) => {
        if(response.status == 200) {
            return response.text()
        }
    })
    .then((html) => {
        var parser = new DOMParser();
        var doc = parser.parseFromString(html, "text/html")
        console.log(doc.head)
        document.head = doc.head
        document.body = doc.body
    })
}

const backgroundLoop = (i = 0) => {
    const FPS = 40;
    var rate; var rotation; var yellow; var red;

    if (i > FPS) i = 0;
    rate = i / FPS * 2 * Math.PI;

    yellow = (30 + Math.sin(rate) * 20);
    red = (100 + Math.sin(rate) * 10);
    rotation = (130 + Math.sin(rate) * 20);

    document.body.style.background = "linear-gradient(" + rotation.toString() + "deg, #f9d423 -" + yellow.toString() + "%, #ff4e50 " + red.toString() + "%)";

    setTimeout(backgroundLoop, 100, i + 1);
}

const onload = () => {
    backgroundLoop()
    main()
}

window.onload = onload