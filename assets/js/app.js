/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.css';


// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';
let path = window.location.pathname;
if(path.includes('sortie/creer')){
    document.getElementById('sortie_lieu').addEventListener("change",afficherLieu);
}
if(path.includes('sortie/creer')){
    document.onload=remember;
}

if(path.includes('sortie/creer')) {
    document.onload = remplir;
}
if(path.includes('sortie/modifier')){
    document.getElementById('sortie_lieu').addEventListener("change",afficherLieu);
}
if(path.includes('lieu/add')){
    document.getElementById('lieu_lieu_ville').addEventListener("change",chercherCoor);
}
if(path.includes('lieu/add')){
    document.getElementById('lieu_rue').addEventListener("blur",chercherCoor);
}



function afficherLieu() {

    let lieu = document.getElementById('sortie_lieu');
    let ville = document.getElementById('ville');
    let rue = document.getElementById('rue');
    let cp = document.getElementById('cp');
    let latitude = document.getElementById('lat');
    let longitude = document.getElementById('long');
    let id = lieu.value
    $.ajax({
        url: "../lieu/recuperer/"+id,
        method: "GET",

    }).done (function(data){
        if (data !=="")
        {

            document.getElementById('labelville').innerText = 'Ville : '
            ville.innerText = data['ville'];
            document.getElementById('labelrue').innerText = 'Rue : '
            rue.innerText = data['rue'];
            document.getElementById('labelcp').innerText = 'Code Postal : '
            cp.innerText = data['cp'];
            document.getElementById('labellat').innerText = 'Latitude : '
            latitude.innerText = data['lat'];
            document.getElementById('labellong').innerText = 'Longitude : '
            longitude.innerText = data['long'];

        }
    }).fail()
    {
        document.getElementById('labelville').innerText = 'Les donnees n\'ont pas pu être récupérées'
    };
















    /*
    let value = lieu.value;
    let json = require('../../public/results.json');
    let ville = document.getElementById('ville');
    let rue = document.getElementById('rue');
    let cp = document.getElementById('cp');
    let latitude = document.getElementById('lat');
    let longitude = document.getElementById('long');

    for (let $i = 0; $i < json.posts.length; $i++) {
        if ((json["posts"][$i].id) == value) {
            console.log(json["posts"][$i]);
            document.getElementById('labelville').innerText = 'Ville : '
            ville.innerText = json["posts"][$i].nomVille;
            document.getElementById('labelrue').innerText = 'Rue : '
            rue.innerText = json["posts"][$i].rue;
            document.getElementById('labelcp').innerText = 'Code Postal : '
            cp.innerText = json["posts"][$i].cp;
            document.getElementById('labellat').innerText = 'Latitude : '
            latitude.innerText = json["posts"][$i].latitude;
            document.getElementById('labellong').innerText = 'Longitude : '
            longitude.innerText = json["posts"][$i].longitude;

            break;
        }
    }
    */

}

function chercherCoor(){
    var lat = document.getElementById('lieu_latitude');
    var long = document.getElementById('lieu_longitude');
    var street = document.getElementById('lieu_rue').value;
    var ville = document.getElementById('lieu_lieu_ville').options[document.getElementById('lieu_lieu_ville').selectedIndex].text;;
    if(ville != ""){
        $.ajax({
            url: "https://nominatim.openstreetmap.org/search", // URL de Nominatim
            type: 'get', // Requête de type GET
            data: "city="+ville+"&street=" +street+"&format=json&addressdetails=1&limit=1&polygon_svg=1" // Données envoyées (q -> adresse complète, format -> format attendu pour la réponse, limit -> nombre de réponses attendu, polygon_svg -> fournit les données de polygone de la réponse en svg)
        }).done(function (response) {
            if(response != ""){
                lat.value = response[0]['lat'];
                long.value = response[0]['lon'];
            }
        }).fail(function (error) {
            alert(error);
        });
    }
}

function remember(){
    document.getElementById("sortie_nom").addEventListener("blur", function()
    {
        sessionStorage.setItem('nom',document.getElementById("sortie_nom").value);
    });
    document.getElementById("sortie_datHeureDebut").addEventListener("change", function()
    {
        sessionStorage.setItem('dd',(document.getElementById("sortie_datHeureDebut").value));
    });
    document.getElementById("sortie_dateLimiteInscription").addEventListener("change",function()
    {
        sessionStorage.setItem('dl',document.getElementById("sortie_dateLimiteInscription").value);
    })
    document.getElementById("sortie_duree").addEventListener("blur",function()
    {
        sessionStorage.setItem('duree',document.getElementById("sortie_duree").value);
    });
    document.getElementById("sortie_infosSortie").addEventListener("blur",function()
    {
        sessionStorage.setItem('infos',document.getElementById("sortie_infosSortie").value);
    });
    document.getElementById("sortie_nbInscriptionsMax").addEventListener("blur",function()
    {
        sessionStorage.setItem('max',document.getElementById("sortie_nbInscriptionsMax").value);
    });
    document.getElementById("sortie_lieu").addEventListener("change",function ()
    {
        sessionStorage.setItem('lieu',document.getElementById("sortie_lieu").value);
    });

}

function remplir(){
    if (sessionStorage.getItem('nom'))
    {
        document.getElementById("sortie_nom").value = (sessionStorage.getItem('nom'));
    }
    if (sessionStorage.getItem('dd'))
    {
        document.getElementById("sortie_datHeureDebut").value = (sessionStorage.getItem('dd'));
    }
    if (sessionStorage.getItem('dl'))
    {
        document.getElementById("sortie_dateLimiteInscription").value = (sessionStorage.getItem('dl'));
    }
    if (sessionStorage.getItem('duree'))
    {
        document.getElementById("sortie_duree").value = (sessionStorage.getItem('duree'));
    }
    if (sessionStorage.getItem('infos'))
    {
        document.getElementById("sortie_infosSortie").value = (sessionStorage.getItem('infos'));
    }
    if (sessionStorage.getItem('max'))
    {
        document.getElementById("sortie_nbInscriptionsMax").value = (sessionStorage.getItem('max'));
    }
    if (sessionStorage.getItem('lieu'))
    {
        document.getElementById("sortie_lieu").value = (sessionStorage.getItem('lieu'));
    }

}

