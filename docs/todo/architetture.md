WebApp
==============

l'applicazione carica sul browser tutto quello che serve per eseguire l'interfaccia grafica, 
e scambia col server solo dati in formato json.
Al primo caricamento viene immagazzinata nella cache del browser i seguenti file:
* immagini sprite dell'interfaccia
* un file css generato da uno script php che comprime lo script
* un file javascript generato in php per comprimere lo script
* un output html5 generato in php contenente la versione della app, e tutte le pagine o finestre che si potranno aprire durante una normale sessione

Questa achitettura permette i seguenti vantaggi:
* velocità nell'eseguire operazioni, in quanto si richiede al server solo i dati necessari al funzionamento, e non intere pagine html o immagini.
* in futuro i sistemi operativi, specie quelli mobili potrebbero supportare queste tecnologie per le sue applicazioni permettendo di creare applicazioni con il minimo sforzo.

TODO
- [ ] cercare un compressore per i file javascript in php semplice e con cache.
- [ ] impostare i file js e css da caricare tramite file .ini e includere negli html (usare i controller js e css per farlo.)
