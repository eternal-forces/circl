const main = () => {
    document.querySelectorAll(".field-placeholder").forEach((e) => {
        e.addEventListener("click", (e) => {
            console.log(e.target.parentElement.children[0])
            e.target.parentElement.children[0].click()
        })
    })

    document.querySelector("button.submit").addEventListener("click", (e) => {
        e.preventDefault() 

        input = document.querySelectorAll("input.input")

        let body = JSON.stringify({
            email: input[0].value,
            password: input[0].value,
        })

        const authenticationRequest = new Request('websites.dekkerwebdesign.com/circl/api/v1/auth', {
            method: 'POST',
            body: body,
            test: "test",
        })

        fetch(authenticationRequest)
        .then((request) => {
            if(request.status == 201) {
                
            }
        })
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

window.onload = () => {
    onload()
}