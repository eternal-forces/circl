const months = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December'
  ] 

  const days = [
    'Sunday',
    'Monday',
    'Tuesday',
    'Wednesday',
    'Thursday',
    'Friday',
    'Saturday'
  ]
function updateTime(_timeElement, _dateElement) {
    console.log("I'm doing something")
    const time = new Date();
    const date = time.getDate()
    const day = days[time.getDay()]
    const month = months[time.getMonth()]
    const formattedDate = `${day}, ${date} ${month}`

    var minutes = time.getMinutes()
    if (minutes < 10) {
        console.log(minutes)
        minutes = "0"+minutes.toString()
    }
    var hours = time.getHours()
    var timething = "AM"

    if (hours > 12) {
        hours -= 12
        timething = "PM"
    }

    const formattedTime = `${hours}:${minutes}` 

    _timeElement.innerHTML = `${hours}:${minutes}<small>${timething}</small>`
    _dateElement.innerHTML = `${day}, ${date} ${month}`
}

function startTime(_timeElement, _dateElement) {
    updateTime(_timeElement, _dateElement)
    setInterval(updateTime, 60000, _timeElement, _dateElement)
}

function start() {
    var timeElement = document.querySelector("div.personalia > div.welcome > div.datetime > div.wrapper > h1.time")
    var dateElement = document.querySelector("div.personalia > div.welcome > div.datetime > div.wrapper > h2.date")
    updateTime(timeElement, dateElement)
    
    const time = new Date();
    console.log(time)
    const timeTillStart = 60000 - time.getMilliseconds() - time.getSeconds()*1000
    console.log(timeTillStart)
    setTimeout(startTime,timeTillStart, timeElement, dateElement)
    var test = [...Array(16)].map(i=>(~~(Math.random()*36)).toString(36)).join('')
    console.log(test)
    console.log(test.length)
}

start()