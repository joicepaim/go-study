package main

import (
	"encoding/json"
	"fmt"
	"io/ioutil"
	"log"
	"net/http"
	"strconv"
)

type funcionarios struct {
	Id        int    `json:"id"`
	Nome      string `json:"nome"`
	Sobrenome string `json:"sobrenome"`
}

func consultar() {
	// fmt.Println(runtime.NumCPU())
	resp, err := http.Get("https://api.ucpvirtual.com.br/api/consultaFuncionarios")
	if err != nil {
		log.Fatal(err)
	}
	body, err := ioutil.ReadAll(resp.Body)
	if err != nil {
		log.Fatal(body)
	}

	// fmt.Printf(body)
	var content []funcionarios

	err = json.Unmarshal(body, &content)
	if err != nil {
		log.Fatal(err)
	}

	for _, nome := range content {
		fmt.Println(nome.Id, " | ", nome.Nome, " ", nome.Sobrenome)
		corrigir(nome.Id)
		fmt.Println("\n")
	}

}
func main() {
	consultar()
}

func corrigir(id int) {
	link := strconv.FormatInt(int64(id), 10)
	respo, erro := http.Get("https://sistema.hostbrs.com.br/api_henry/corrigir_pontos/" + link)
	if erro != nil {
		log.Fatal(erro)
	}

	body, err := ioutil.ReadAll(respo.Body)
	if err != nil {
		log.Fatal(body)

	}

	fmt.Println(string(body))
}
