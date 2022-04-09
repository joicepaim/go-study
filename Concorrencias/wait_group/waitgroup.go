package main

import (
	"fmt"
	"sync"
	"time"
)

func main() {
	var waitgroup sync.WaitGroup
	waitgroup.Add(3)
	go func() {
		escrever("batata doce")
		waitgroup.Done() //-1
	}()
	go func() {
		escrever("Abacate")
		waitgroup.Done() //-1
	}()
	go func() {
		escrever("batata fritaa")
		waitgroup.Done() //-1
	}()
	waitgroup.Wait()

}
func escrever(texto string) {
	for i := 0; i < 5; i++ {

		fmt.Println(texto)
		time.Sleep(time.Second)

	}

}
