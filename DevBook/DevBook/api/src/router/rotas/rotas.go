package rotas

import (
	"net/http"

	"api/src/middlewares.go"

	"github.com/gorilla/mux"
)

// representa todas as rotas da api
type Rota struct {
	URI                string
	Metodo             string
	Funcao             func(http.ResponseWriter, *http.Request)
	RequerAutenticacao bool
}

func Configurar(r *mux.Router) *mux.Router {
	rotas := rotasUsuarios
	rotas = append(rotas, rotaLogin)           // coloca mais um item no slice
	rotas = append(rotas, rotasPublicacoes...) // coloca mais de um item no slice
	for _, rota := range rotas {
		if rota.RequerAutenticacao {
			r.HandleFunc(rota.URI, middlewares.Autenticar(rota.Funcao)).Methods(rota.Metodo)

		} else {
			r.HandleFunc(rota.URI, middlewares.Loger(rota.Funcao)).Methods(rota.Metodo)
		}
	}
	return r
}
