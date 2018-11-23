function closeModal() {
    document.getElementById('id01').style.display = 'none';
}

function chooseContest(username, id) {
    document.getElementById('idGara').value = id;
    document.getElementById('id01').style.display = 'block';
    requestAvailableDogs(username, id);
}

function chooseDog(id) {
    document.getElementById('idCane').value = id;
    var list = document.getElementsByClassName('card');
    for(var i = 0; i < list.length; i++) {
        if(list[i].classList.contains('selected')) list[i].className = 'card';
    }
    document.getElementById(id).classList.add('selected');
}