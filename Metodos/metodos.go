package main

import "fmt"

type usuario struct {
	nome  string
	idade uint
}

func (u usuario) salvar() {
	fmt.Printf("Salvando os dados %s ", u.nome)
}
func (u usuario) maiorDeIdade() bool {
	return u.idade >= 18
}
func (u *usuario) fazerAniver() {
	u.idade++
}
func main() {
	u := usuario{"Joice", 18}
	u.salvar()

	if u.maiorDeIdade() {
		fmt.Println("Maior")
	} else {
		fmt.Println("Menor")
	}

	u.fazerAniver()
	fmt.Println(u.idade)

}
