// - O código abaixo é linear. Como fazer as duas funções rodarem concorrentemente?
//     - https://play.golang.org/p/XP-ZMeHUk4
// - Goroutines!
// - O que são goroutines? São "threads."
// - O que são threads? [WP](https://pt.wikipedia.org/wiki/Thread_...))
// - Na prática: go func.
// - Exemplo: código termina antes da go func executar.
// - Ou seja, precisamos de uma maneira pra "sincronizar" isso.
// - Ah, mas então... não.
// - Qualé então? sync.WaitGroup:
// - Um WaitGroup serve para esperar que uma coleção de goroutines termine sua execução.
//     - func Add: "Quantas goroutines?"
//     - func Done: "Deu!"
//     - func Wait: "Espera todo mundo terminar."
// - Ah, mas então... sim!
// - Só pra ver: runtime.NumCPU() & runtime.NumGoroutine()
package main

import (
	"fmt"
	"runtime"
	"time"
)

func main() {
	runtime.GOMAXPROCS(runtime.NumCPU() * 4)
	println("Num processamento ", runtime.NumCPU())
	channel := make(chan int)
	go func() {
		for i := 0; i < runtime.NumCPU(); i++ {
			go worker(channel)
		}
	}()

	for i := 0; i <= 100; i++ {
		channel <- i
	}

}

func worker(channel chan int) { // vai imprimir os dados
	for i := range channel {
		fmt.Println(i)
		time.Sleep(time.Second * 3)
	}
}
