jQuery(document).ready(function($) {
  // Basisverzeichnis für Bilder (leer, da Bilder in adventskalenderdaten.js vollständig referenziert sind)
  const basePath = '';

  var backgroundImageUrlLarge = '';
  var backgroundImageUrlSmall = '';
  var showPastPopups = true;
  var showPastImages = true;
  var labelAdditionalInfoLink = 'Klick mich';
  var headerPopupNotTime = 'Noch etwas Geduld!';

    if (typeof ffwAdventskalenderBackgroundImages !== 'undefined') {
        backgroundImageUrlLarge = ffwAdventskalenderBackgroundImages.large || '';
        backgroundImageUrlSmall = ffwAdventskalenderBackgroundImages.small || '';
    }

    if (typeof ffwAdventskalenderAdditionalSettings !== 'undefined') {
        showPastPopups = ffwAdventskalenderAdditionalSettings.show_past_popups;
        showPastImages = ffwAdventskalenderAdditionalSettings.show_past_images;
        labelAdditionalInfoLink= ffwAdventskalenderAdditionalSettings.info_button_label;
        headerPopupNotTime = ffwAdventskalenderAdditionalSettings.not_open_label;
    }

  // Aktuelles Datum
  let currentDate = new Date().getDate();
  let currentMonth = new Date().getMonth();

  // Monatsnamen
  const monthNames = ["Januar", "Februar", "März", "April", "Mai", "Juni",
                      "Juli", "August", "September", "Oktober", "November", "Dezember"];

  // HTML-Elemente für das Modal
  const modalImage = document.getElementById('modalImage');
  const modalText = document.getElementById('modalText');
  const modalLink = document.getElementById('modalLink');
  const modalTitle = document.getElementById('imageModalLabel');

  const notTimeModalBody = document.getElementById('notTimeModalBody');
  const notTimeModalLabel = document.getElementById('notTimeModalLabel');
  const notTimeModal = new bootstrap.Modal(document.getElementById('notTimeModal'), {keyboard: true,  backdrop: false });

    // Funktion zum Anzeigen des Modals für ein Türchen
    function showModal(dayNumber, currentDate, dayInfo) {
        modalTitle.textContent = `Adventskalender Türchen ${dayNumber}`;
        modalImage.src = dayInfo.image;
        modalText.innerHTML = dayInfo.text;
        modalLink.href = dayInfo.link;
        modalLink.title= labelAdditionalInfoLink;
        modalLink.textContent = labelAdditionalInfoLink;
        modalLink.style.display = dayInfo.link ? 'inline-block' : 'none';
        if(!showPastPopups && currentDate != dayNumber) {
            return;
          }
        const imageModal = new bootstrap.Modal(document.getElementById('imageModal'), { keyboard: true, backdrop: false });
        imageModal.show();
      }
  // Hintergrundbild für den Adventskalender festlegen
  function setCalendarBackground() {
      const adventCalendar = document.querySelector('.advent-calendar');
      const windowWidth = window.innerWidth;

      if (windowWidth >= 992) {
          adventCalendar.style.backgroundImage = `url('${backgroundImageUrlLarge}')`;
      } else {
          adventCalendar.style.backgroundImage = `url('${backgroundImageUrlSmall}')`;
      }
  }

  setCalendarBackground();
  window.addEventListener('resize', setCalendarBackground);

  // Alle Türchen auswählen und Event-Handler hinzufügen
  document.querySelectorAll('.day').forEach(day => {
      const dayNumber = parseInt(day.textContent, 10);

      // AJAX-Anfrage, um Daten für das Türchen zu erhalten
      var ajaxurl = ajax_object.ajaxurl;
      $.ajax({
          url: ajaxurl, // Stellen Sie sicher, dass 'ajaxurl' korrekt definiert ist
          type: 'POST',
          dataType: 'json',
          data: {
              'action': 'get_door_data',
              'day': dayNumber
          },
          success: function(response) {

              if (response && currentMonth === 11 && dayNumber <= currentDate) {
                    if(showPastImages && dayNumber != currentDate) {
                        day.classList.add('opened');
                        response.image = (undefined === response.image || '' === response.image)? '' : response.image;
                        day.style.backgroundImage = `url('${response.image}')`;
                    }
                  // Speichern der Antwortdaten direkt am Element
                  day.dataset.response = JSON.stringify(response);
              }
          }
      });

      day.addEventListener('click', function () {
           /*if (currentMonth !== 11 || dayNumber > currentDate) {
                console.log('currentMonth: ' + currentMonth + ' dayNumber: ' + dayNumber + ' currentDate: ' +currentDate);
                notTimeModal.show();
                return;
            }*/
            // Abrufen der gespeicherten Antwortdaten
            let response = {};
            if (this.dataset.response) {
                response = JSON.parse(this.dataset.response);
            }
            if (response) {
                var selectedMonth = currentMonth; 
                if (selectedMonth <= 10) {
                    //Anzeige beim Klick auf ein Türchen wenn der Monat noch nicht Dezember ist, HTML Tags möglich
                    notTimeModalBody.innerHTML = `Heute ist der ${currentDate}. ${monthNames[selectedMonth]}! Die Türchen lassen sich erst ab dem 1. Dezember öffnen...`;
                    notTimeModalLabel.textContent = headerPopupNotTime;
                    notTimeModal.show();
                  } else if (selectedMonth === 11 && currentDate >= 25) {
                    notTimeModalBody.innerHTML = `Heute ist bereits der ${currentDate}. Dezember, d.h. Weihnachten ist vorüber. Nächstes Jahr öffnet sich unser Kalender wieder! `;

                    notTimeModalLabel.textContent = headerPopupNotTime;
                    notTimeModal.show();
                  } else if (selectedMonth === 11 && dayNumber === currentDate) {
                    // Zeige das Bild für das aktuelle Türchen immer nach einem Klick an und füge roten Rand hinzu
                    day.classList.add('current');
                    day.classList.add('opened');
                    day.style.backgroundImage = `url('${response.image}')`;
                    showModal(dayNumber, currentDate, response);
                  } else if (selectedMonth === 11 && dayNumber < currentDate) {
                    // Zeige das Modal für vergangene Türchen, wenn showModalForPastDoors true ist
                    showModal(dayNumber, currentDate, response);
                  } else if (selectedMonth === 11 && dayNumber > currentDate) {
                    // Berechne die Anzahl der Tage bis zum Öffnen des Türchens
                    const daysUntilOpen = dayNumber - currentDate;
                    if (daysUntilOpen === 1) {
                      notTimeModalBody.innerHTML = `Morgen ist es endlich soweit und du kannst dieses Türchen öffnen. Bis dahin musst Du aber noch warten...`;
                    } else {
                      if (dayNumber === 24) {
                        notTimeModalBody.innerHTML = `Schön wäre es sicherlich, wenn heute schon Weihnachten wäre! Aber bis dahin musst Du noch ${daysUntilOpen} Tage warten.`;
                      } else {
                        notTimeModalBody.innerHTML = `Noch etwas Geduld is angesagt! Du musst noch <strong>${daysUntilOpen} Tage</strong> warten, bis du dieses Türchen öffnen kannst.`;
                      }
                    }
                    notTimeModalLabel.textContent = headerPopupNotTime;
                    notTimeModal.show();
                  }
            }
      });
  });
});
