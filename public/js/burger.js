// console.log("hello world") inscrit après la création du fichier, pour s'assurer que le fichier soit bien prit en compte par le navigateur


// Création d'événement click
const burger = document.getElementById('burger');
const menu = document.getElementById('principal');
 
burger.addEventListener('click', () => {
    //Fonction apparition du menu nav
    if (menu.style.display === "flex") {

        menu.style.display = "none";

    } else {

        menu.style.display = "flex";
    }
})


    //Switch de bars vers croix et inversement (ouvrir et fermer le menu)
const lines = document.querySelector('.lines');
const cross = document.querySelector('.cross');

burger.addEventListener('click', () => {
    
    if (lines.style.display === "none") {

        lines.style.display = "block";

    } else {

        lines.style.display = "none";
    }
})

burger.addEventListener('click', () => {

    if (cross.style.display === "block") {

        cross.style.display = "none";

    } else {
        
        cross.style.display = "block"
    }
})