// Observateurs d'evenements jQuery pour la calculette : les $.getJSON() vont chercher les pages getDataPaliers, et récupère les données de paliers pour les transmettre aux fonctions de calcul
$(function () {
    // TODO $('#formDossier').reset();
    $('#loading').hide();
    $("#file_description").hide();
    $("#detailPages").hide();
    $("#uploadPDF").change(function () {
        let fichier = $('#uploadPDF').prop('files')[0];
        if (fichier != undefined) {
            var form_data = new FormData();
            form_data.append('file', fichier);
            $.ajax({
                type: 'POST',
                url: 'models/Traitement_Donnees_Compte_Pages.php',
                contentType: false,
                processData: false,
                data: form_data,
                success: function (reponse) {
                    if (reponse == 'failure' || reponse == 'notPDF' || reponse == 'tooHeavy') {
                        $("#detailPages").hide();
                        $("#file_description").hide();
                        $('#loading').hide();
                        if (reponse == 'failure') {
                            alert('Le traitement du fichier à échoué');
                        } else if (reponse == 'notPDF') {
                            alert('Le fichier n\'est pas un PDF.');
                        } else if (reponse == 'tooHeavy') {
                            alert('Le fichier envoyé est trop lourd.');
                        }
                        var zero = "0";
                        $("#uploadPDF").replaceWith($("#uploadPDF").val('').clone(true)); //Reset valeur de l'upload
                        $("#nbPages").html(zero); //Reset toute les valeurs à 0
                        $("#nbPagesC").attr("value", "0").attr("placeholder", "0");
                        $("#nbPagesNB").attr("value", "0").attr("placeholder", "0");
                    } else {
                        var obj = JSON.parse(reponse);
                        if (obj.NbPagesNB == obj.NbPages) {
                            var paragInfo = "Ce document comporte " + obj.NbPages + " pages, toutes en noir et blanc. <br>";
                        } else if (obj.NbPagesC == obj.NbPages) {
                            var paragInfo = "Ce document comporte " + obj.NbPages + " pages, toutes en couleur.<br>";
                        } else {
                            var paragInfo = "Ce document comporte " + obj.NbPages + " pages, dont " + obj.NbPagesC + " en couleurs et " + obj.NbPagesNB + " en noir et blanc.<br>";
                        }
                        $("#detailPages").show();
                        $("#file_description").show().html(paragInfo);
                        $("#nomFichier").html(fichier.name);
                        $("#nbPages").html(obj.NbPages);
                        $("#nbPagesC").html(obj.NbPagesC);
                        $("#nbPagesNB").html(obj.NbPagesNB);
                        calculDevis(jsonData);
                    }
                },
                beforeSend: function () {
                    $('#loading').show();
                    $('#file_description').hide();
                },
                complete: function () {
                    $('#loading').hide();
                }
            });
            //$("#erreur").hide();
        }
    });

    var jsonData = null;
    $.getJSON("models/getDataPaliersNB.php", function (paliersNB) {
        $.getJSON("models/getDataPaliersC.php", function (paliersC) {
            $.getJSON("models/getDataFC.php", function (paliersFC) {
                $.getJSON("models/getDataFT.php", function (paliersFT) {
                    $.getJSON("models/getMaxSpiplast.php", function (maxSpiplast) {
                        $.getJSON("models/getMaxSpimetal.php", function (maxSpimetal) {
                            $.getJSON("models/getMaxThermo.php", function (maxThermo) {
                                $.getJSON("models/getPaliersSpiplast.php", function (paliersSpiplast) {
                                    $.getJSON("models/getPaliersSpimetal.php", function (paliersSpimetal) {
                                        $.getJSON("models/getPaliersThermo.php", function (paliersThermo) {
                                            jsonData = {
                                                'paliersNB': paliersNB,
                                                'paliersC': paliersC,
                                                'paliersFC': paliersFC,
                                                'paliersFT': paliersFT,
                                                'maxSpiplast': maxSpiplast,
                                                'maxSpimetal': maxSpimetal,
                                                'maxThermo': maxThermo,
                                                'paliersSpiplast': paliersSpiplast,
                                                'paliersSpimetal': paliersSpimetal,
                                                'paliersThermo': paliersThermo
                                            };
                                        })
                                    })
                                })
                            })
                        })
                    })
                })
            })
        })
    });


    // Couleurs non selectionnables si pas de FC selectionnée
    $('#couvCouleurFC :radio, #dosCouleurFC :radio').prop('checked', false).prop('disabled', true);

    $("#dossier").on('click', function () {
        $('#btnFTCouv').prop('checked', true).prop('disabled', true);
        $('#btnFTDos').prop('checked', false).prop('disabled', true);
        $('#btnFCCouv').prop('checked', false).prop('disabled', true);
        $('#btnFCDos').prop('checked', true).prop('disabled', true);
        $('#couvCouleurFC :radio').prop('checked', false).prop('disabled', true);
        $('#dosCouleurFC :radio').prop('disabled', false);
        $('#thermo, #spiplast, #spimetal').prop('checked', false).prop('disabled', false);
    });
    $("#memoire").on('click', function () {
        $('#btnFTCouv').prop('checked', true).prop('disabled', true);
        $('#btnFTDos').prop('checked', false).prop('disabled', true);
        $('#btnFCCouv').prop('checked', true).prop('disabled', true);
        $('#btnFCDos').prop('checked', true).prop('disabled', true);
        $('#couvCouleurFC :radio').prop('disabled', false);
        $('#dosCouleurFC :radio').prop('disabled', false);
        $('#thermo, #spiplast, #spimetal').prop('checked', false).prop('disabled', false);
    });
    $("#these").on('click', function () {
        $('#btnFTCouv').prop('checked', true).prop('disabled', false);
        $('#btnFTDos').prop('checked', true).prop('disabled', false);
        $('#btnFCCouv').prop('checked', true).prop('disabled', true);
        $('#btnFCDos').prop('checked', true).prop('disabled', true);
        $('#couvCouleurFC :radio').prop('disabled', false);
        $('#dosCouleurFC :radio').prop('disabled', false);
        $('#thermo').prop('checked', true).prop('disabled', true);
        $('#spiplast, #spimetal').prop('checked', false).prop('disabled', true);
    });
    $("#perso").on('click', function () {
        $('#btnFTCouv').prop('checked', false).prop('disabled', false);
        $('#btnFTDos').prop('checked', false).prop('disabled', false);
        $('#btnFCCouv').prop('checked', false).prop('disabled', false);
        $('#btnFCDos').prop('checked', false).prop('disabled', false);
        $('#couvCouleurFC :radio').prop('disabled', false);
        $('#dosCouleurFC :radio').prop('disabled', false);
        $('#thermo, #spiplast, #spimetal').prop('checked', false).prop('disabled', false);
    });

    // Relance le calcul du devis 
    $("#thermo, #spiplast, #spimetal, #btnFTCouv, #btnFTDos, #btnFCCouv, #btnFCDos, #quantity, #rectoverso, #perso, #these, #memoire, #dossier").on('click', function () {
        calculDevis(jsonData);
    });

    // Prevent form validation
    $("body").keypress(function (e) {
        if (e.keyCode == 13) {
            e.preventDefault();
        }
    });
});

function calculDevis(jsonData) {
    let quantity = $("#quantity").val();
    let totalNB = Number(calculPages('NB', jsonData['paliersNB'], quantity));
    let totalC = Number(calculPages('C', jsonData['paliersC'], quantity));
    let totalCouvFC = Number(calculCouvFC(jsonData['paliersFC']));
    let totalCouvFT = Number(calculCouvFT(jsonData['paliersFT']));
    let totalR = Number(calculReliure(jsonData['maxSpiplast'], jsonData['maxSpimetal'], jsonData['maxThermo'], jsonData['paliersSpiplast'], jsonData['paliersSpimetal'], jsonData['paliersThermo']));
    let total = Number(totalNB + totalC + totalR + totalCouvFC + totalCouvFT).toFixed(2);
    $("#devisTotal").html(total);
}

// Calcule le prix des pages noir&blanc ou couleur
function calculPages(type, paliers, quantity) {
    let nbPages = 0;
    let zone = '';
    let total = 0;
    let nbTotPages = 0;
    let prixU = 0;
    let i = 0;

    if (type === 'NB') {
        nbPages = $("#nbPagesNB").text();
        zone = "#devisPagesNB";
    } else if (type === 'C') {
        nbPages = $("#nbPagesC").text();
        zone = "#devisPagesC";
    }

    if (quantity < 1) {
        quantity = 1;
        alert('Veuillez indiquer 1 exemplaire minimum.');
    }

    nbTotPages = nbPages * quantity;

    while (paliers[i + 1] && (nbTotPages > paliers[i]['palier'])) {
        i++;
    }
    prixU = Number(paliers[i]["prix"]).toFixed(2);
    total = Number(nbTotPages * prixU).toFixed(2);

    $(zone + "Quant").html(nbTotPages);
    $(zone + "PrixU").html(prixU);
    $(zone + "Total").html(total);
    return total;
}

// Calcul du prix des reliures
function calculReliure(maxSpiplast, maxSpimetal, maxThermo, paliersSpiplast, paliersSpimetal, paliersThermo) {
    let zone = '#devisReliure';
    let quantity = $("#quantity").val();
    let nbFeuilles = Number($("#nbPages").text()); // pages N&B et Couleur SANS recto-verso
    let maxFeuillesThermo = maxThermo[0]['sValue'];
    let maxFeuillesPlast = maxSpiplast[0]['sValue'];
    let maxFeuillesMetal = maxSpimetal[0]['sValue'];
    let total = 0;
    let prixU = 0;
    let i = 0;

    if ($("#rectoverso").prop('checked')) {
        nbFeuilles = Math.round(nbFeuilles / 2); // pas besoin de modulo % car .5 est arrondi au-dessus
    }

    if ($('#spiplast').prop('checked')) {
        if (nbFeuilles > maxFeuillesPlast) {
            alert("Les spirales plastiques ne sont disponibles que pour " + maxFeuillesPlast + " pages maximum.\nAlternatives : recto-verso ou diviser le document en plusieurs parties.");
            $('#spiplast').prop('checked', false)
        }
        while (paliersSpiplast[i + 1] && (nbFeuilles > paliersSpiplast[i]['palier'])) {
            i++;
        }
        prixU = Number(paliersSpiplast[i]["prix"]).toFixed(2);
        total = Number(quantity * prixU).toFixed(2);
    }
    if ($('#spimetal').prop('checked')) {
        if (nbFeuilles > maxFeuillesMetal) {
            alert("Les spirales métalliques ne sont disponibles que pour " + maxFeuillesMetal + " pages maximum.\nAlternatives : recto-verso ou diviser le document en plusieurs parties.");
            $('#spimetal').prop('checked', false)
        }
        while (paliersSpimetal[i + 1] && (nbFeuilles > paliersSpimetal[i]['palier'])) {
            i++;
        }
        prixU = Number(paliersSpimetal[i]["prix"]).toFixed(2);
        total = Number(quantity * prixU).toFixed(2);
    }
    if ($('#thermo').prop('checked')) {
        if (nbFeuilles > maxFeuillesThermo) {
            alert("La reliure thermocollée n'est disponible que pour " + maxFeuillesThermo + " pages maximum.\nAlternatives : recto-verso ou diviser le document en plusieurs parties.");
            $('#thermo').prop('checked', false)
        }
        while (paliersThermo[i + 1] && (nbFeuilles > paliersThermo[i]['palier'])) {
            i++;
        }
        prixU = Number(paliersThermo[i]["prix"]).toFixed(2);
        total = Number(quantity * prixU).toFixed(2);
    }

    $(zone + "Quant").html(quantity);
    $(zone + "PrixU").html(prixU);
    $(zone + "Total").html(total);

    return total;
}

//Calcul des Feuilles Cartonnées en première et quatrièmes de couverture.
function calculCouvFC(dataFC) {
    let quantity = $("#quantity").val();
    let nbFC = 0;
    let total = 0;
    let zone = "#devisFC";
    let prixU = Number(dataFC[0]["sValue"]).toFixed(2);

    if ($('#btnFCCouv').prop('checked')) {
        nbFC++;
    }
    if ($('#btnFCDos').prop('checked')) {
        nbFC++;
    }
    if (quantity > 1) {
        nbFC *= quantity;
    }

    total = Number(prixU * nbFC).toFixed(2);

    $(zone + "Quant").html(nbFC);
    $(zone + "PrixU").html(prixU);
    $(zone + "Total").html(total);

    return total;
}

//Calcul des Feuillets Transparents en première et quatrièmes de couverture.
function calculCouvFT(dataFT) {
    let quantity = $("#quantity").val();
    let nbFT = 0;
    let total = 0;
    let zone = "#devisFT";
    let prixU = Number(dataFT[0]["sValue"]).toFixed(2);

    if ($('#btnFTCouv').prop('checked')) {
        nbFT++;
    }
    if ($('#btnFTDos').prop('checked')) {
        nbFT++;
    }
    if (quantity > 1) {
        nbFT *= quantity;
    }

    total = Number(prixU * nbFT).toFixed(2);

    $(zone + "Quant").html(nbFT);
    $(zone + "PrixU").html(prixU);
    $(zone + "Total").html(total);

    return total;
}

// // Calcul de la TVA
// let TVA = 0;
// if (document.getElementById("TVA").value != 1) {
//     if (document.getElementById("TVA").value == 2) {
//         TVA = 0.055;
//     } else if (document.getElementById("TVA").value == 3) {
//         TVA = 0.1;
//     } else if (document.getElementById("TVA").value == 4) {
//         TVA = 0.2;
//     }
// }

// let totalZ = Number(parseFloat(zone1) + parseFloat(zone2) + parseFloat(zone3) + parseFloat(zone4) + parseFloat(zone5) + parseFloat(zone8) + parseFloat(zone9) + parseFloat(zone10) + parseFloat(zone11) + parseFloat(zone12));
// let totalY = totalZ;

// window.document.getElementById("zoneTVA").innerHTML = (totalZ * TVA).toFixed(2);

// if (totalZ) {
//     window.document.getElementById("zone6").value = totalZ.toFixed(2) + "€";
// } else {
//     window.document.getElementById("zone6").value = "0.00€";
// }
// window.document.getElementById("zoneRe").innerHTML = "Remise étudiante: - " + (totalY - totalZ).toFixed(2) + "€";

// //Remise étudiante 10%
// if (document.getElementById("remiseEtudiant").checked == true) {
//     window.document.getElementById("zone6").value = (totalZ * 0.90).toFixed(2) + "€";
//     window.document.getElementById("zoneRe").innerHTML = "Remise étudiante: - " + (totalY - totalZ * 0.90).toFixed(2) + "€";
// }
// }