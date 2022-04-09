package main

import (
	"encoding/json"
	"fmt"
	"io/ioutil"
	"log"
	"net/http"
)

type api struct {
	Page       int    `json:"page"`
	PerPage    int    `json:"per_page"`
	Total      int    `json:"total"`
	TotalPages int    `json:"total_pages"`
	Data       []user `json:"data"`
	Ad         ad     `json:"ad"`
}

type user struct {
	ID        int    `json:"id"`
	Email     string `json:"email"`
	FirstName string `json:"first_name"`
	LastName  string `json:"last_name"`
	Avatar    string `json:"avatar"`
}

type ad struct {
	Company string `json:"company"`
	URL     string `json:"url"`
	Text    string `json:"text"`
}

func pegarDados() {
	resp, err := http.Get("https://reqres.in/api/users")
	if err != nil {
		log.Fatal(err)
	}
	body, err := ioutil.ReadAll(resp.Body)
	if err != nil {
		log.Fatal(body)
	}

	content := api{}

	err = json.Unmarshal(body, &content)
	if err != nil {
		log.Fatal(err)
	}

	fmt.Printf("Page: %v\n", content.Page)
	fmt.Printf("Per page: %v\n", content.PerPage)
	fmt.Printf("Total: %v\n", content.Total)
	fmt.Printf("Total pages: %v\n", content.TotalPages)

	for _, c := range content.Data {
		fmt.Printf("E-mail: %v\n", c.Email)
	}
}

func main() {
	resp, err := http.Get("https://reqres.in/api/users")
	if err != nil {
		log.Fatal(err)
	}

	body, err := ioutil.ReadAll(resp.Body)
	if err != nil {
		log.Fatal(body)
	}

	content := api{}

	err = json.Unmarshal(body, &content)
	if err != nil {
		log.Fatal(err)
	}

	fmt.Printf("Page: %v\n", content.Page)
	fmt.Printf("Per page: %v\n", content.PerPage)
	fmt.Printf("Total: %v\n", content.Total)
	fmt.Printf("Total pages: %v\n", content.TotalPages)

	for _, c := range content.Data {
		fmt.Printf("E-mail: %v\n", c.Email)
	}
}

// Make a request for a user with a given I
