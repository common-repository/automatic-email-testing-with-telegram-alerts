// Function to update the current UTC time
function aetwtaha4cca_updateCurrentTime() {
    const currentUtcTime = new Date(); // Get the current UTC time
    document.getElementById("aetwtaha4cca-current-server-time").textContent = currentUtcTime.toISOString().replace('T', ' ').substring(0, 19);
}

// Function to update the countdown timer
function aetwtaha4cca_updateCountdown() {
    // Get the current time from the HTML element
    const currentTimeText = document.getElementById("aetwtaha4cca-current-server-time").textContent;
    const nextSendText = document.getElementById("aetwtaha4cca-next-email-send").textContent;

    // Convert to Date objects
    const currentTime = new Date(currentTimeText);
    const nextSendTime = new Date(nextSendText);

    const timeDiff = nextSendTime.getTime() - currentTime.getTime();

    if (timeDiff > 0) {
        const hours = Math.floor((timeDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);
        document.getElementById("aetwtaha4cca-countdown-timer").textContent = hours + "h " + minutes + "m " + seconds + "s";
    } else {
        document.getElementById("aetwtaha4cca-countdown-timer").textContent = "Email Currently Being Sent!";
    }
}

// Update both the current time and countdown every second
setInterval(() => {
    aetwtaha4cca_updateCurrentTime();
    aetwtaha4cca_updateCountdown();
}, 1000); // Update every second

// Initial call to set the time on page load
aetwtaha4cca_updateCurrentTime();
