package main

import (
	"html/template"
	"log"
	"net/http"
)

var templates *template.Template

type usuario struct {
	Nome  string
	Email string
	idade uint
}

func main() {
	templates = template.Must(template.ParseGlob("*html")) // Guarda na variavel templates, todos os arquis .html
	u := usuario{"Joice", "joice@gmail.com", 18}

	http.HandleFunc("/home", func(w http.ResponseWriter, r *http.Request) {
		templates.ExecuteTemplate(w, "pagina.html", u)
	})

	http.HandleFunc("/users", func(w http.ResponseWriter, r *http.Request) {
		w.Write([]byte("pagina de users!"))
	})

	log.Fatal(http.ListenAndServe(":8080", nil))
}
