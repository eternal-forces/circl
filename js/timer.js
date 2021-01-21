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
    const time = new Date();
    const date = time.getDate()
    const day = days[time.getDay()]
    const month = months[time.getMonth()]
    const formattedDate = `${day}, ${date} ${month}`

    var minutes = time.getMinutes()
    if (minutes < 10) {
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
    const timeTillStart = 60000 - time.getMilliseconds() - time.getSeconds()*1000
    setTimeout(startTime,timeTillStart, timeElement, dateElement)
}

export {start};