package main

type Flor struct {
	Nome string
}

func (f *Flor) morreu() {
	f.Nome = "Azaleia"

}

func main() {
	flor := Flor{Nome: "Girassol"}
	flor.morreu()
	println(flor.Nome)
}

// A := 10
// &A  pega o endereço de memória da variável
// Var ponteiro *int = &a
// Um ponteiro aponta para o endereço de memória de determinada variavel
// Fmt.Println(ponteiro) // printa o endereço da memoria
// Fmt.Println(*ponteiro
