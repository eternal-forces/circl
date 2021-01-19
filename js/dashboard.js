import * as timer from "./timer.js"
import * as tasks from "./tasks.js"

window.onload = () => {
    timer.start()
    tasks.onLoad()
}
