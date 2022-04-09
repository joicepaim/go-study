package main

import (
	"crud/servidor"
	"log"
	"net/http"

	"github.com/gorilla/mux"
)

func main() {
	router := mux.NewRouter()

	router.HandleFunc("/usuarios", servidor.CriarUser).Methods(http.MethodPost)
	router.HandleFunc("/usuarios", servidor.ConsultarUsers).Methods(http.MethodGet)
	router.HandleFunc("/usuarios/{id}", servidor.ConsultarUser).Methods(http.MethodGet)
	router.HandleFunc("/atualizar/{id}", servidor.AtualizarUser).Methods(http.MethodPut)
	router.HandleFunc("/deletar/{id}", servidor.DeletarUser).Methods(http.MethodDelete)
	log.Fatal(http.ListenAndServe(":8080", router))

}
