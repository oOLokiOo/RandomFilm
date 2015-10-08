package main

import (
	"net/http"
	"html/template"
	"fmt"
	"encoding/xml"
	"io/ioutil"
)


type Movies struct {
	XMLName 	xml.Name 	`xml:"movies"`
	FilmsList 	[]Film 		`xml:"film"`
}

type Film struct {
	Ru 			string 	`xml:"ru"`
	En 			string 	`xml:"en"`
	Year 		string 	`xml:"year"`
	Kinopoisk 	string 	`xml:"kinopoisk"`
	Imdb 		string 	`xml:"imdb"`
}

const (
	GOOGLE_IMAGES_URL 	string 	= "http://ajax.googleapis.com/ajax/services/search/images?v=1.0&imgsz=large&rsz=8&q="
	EN_SEARCH_PREFIX 	string 	= " film poster"
	RU_SEARCH_PREFIX 	string 	= " фильм постер"
	//BLOCKED_RESOURCES []string = {"www.impawards.com", "en.wikipedia.org"}
	XML_PATH 			string 	= "../../users/1/films.xml"
)


func index(w http.ResponseWriter, r *http.Request) {
	//BLOCKED_RESOURCES := []string {"www.impawards.com", "en.wikipedia.org"}
	//fmt.Println("APP: ", BLOCKED_RESOURCES)
/*
	xmlFile, err := os.Open("../../users/1/films.xml")
	if err != nil {
		fmt.Println("\n\nERROR: ", err)
		return
	}
	defer xmlFile.Close()

	var m Movies
*/
	m := Movies{}

	xmlFile, _ := ioutil.ReadFile(XML_PATH)
	err := xml.Unmarshal(xmlFile, &m)
	if err != nil {
		panic(err)
		return
	}

	//fmt.Println(m.FilmsList[0].Ru)
	for _, film := range m.FilmsList {
		fmt.Printf("\t%s\n", film)
	}


	w.Header().Set("Content-type", "text/html")
	
	t, err := template.ParseFiles("index.html")
	if err != nil { 
		fmt.Println("\n\nERROR: ", err)
		return
	}
	t.Execute(w, nil)
}


func main() {
	http.HandleFunc("/", index)

	http.ListenAndServe(":80", nil)
}