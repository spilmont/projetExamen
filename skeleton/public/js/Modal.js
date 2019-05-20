document.querySelectorAll('a#nom').forEach(function (a) {

    a.addEventListener("click", function () {
        event.preventDefault();


        axios.get("/admin/ajaxlink/" + this.innerHTML).then(function (response) {

            console.log(response);
            document.getElementById('modal').style.display = "block";


            if ( response.data.idgrade !== "hidden") {
                document.getElementById('modal').innerHTML =
                    "<img class='ico' src='https://img.icons8.com/metro/26/000000/delete-sign.png' alt='close'>" +
                    "<div>" + response.data.nom + " " + response.data.prenom + "</div><br><br>" +
                    "<a href='/admin/update/" + response.data.id + "'> modifier</a><br>" +
                    "<a href='/admin/message/" + response.data.id + "'>messagerie</a><br>" +
                    "<a id='noter' href='admin/" + response.data.idgrade + "/gradetouser'>noter</a>"
            }else{
                document.getElementById('modal').innerHTML =
                    "<img class='ico' src='https://img.icons8.com/metro/26/000000/delete-sign.png' alt='fermer la fenetre'>" +
                    "<div>" + response.data.nom + " " + response.data.prenom + "</div><br><br>" +
                    "<a href='/admin/update/" + response.data.id + "'> modifier</a><br>"
            }
        })
    })
});

document.addEventListener("keypress", function (e) {

    var keynum = e.which;
    if (keynum === 27) {

        document.getElementById('modal').style.display = "none";

    }

});


document.getElementById('modal').addEventListener("click", function () {


    document.getElementById('modal').style.display = "none";


});

