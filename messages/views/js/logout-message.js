const logoutLabel = document.getElementById('logout-label');

logoutLabel.addEventListener( 'click', () => {
    fetch('../../authentication/services/logout.php')
    .then(() => {
        window.location.href = '../../authentication/views/login.html';
    })
    .catch((error) => {
        console.error("Logout failed:", error);
    });
})