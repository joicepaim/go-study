package main

import (
	"fmt"
	"reflect"
)

func main() {
	var array1 [5]string
	fmt.Println(array1)
	array2 := [5]string{"posição 1", "p2", "p3"}
	fmt.Println(array2)

	array3 := [...]int{1, 2, 3, 4} // não deixa com taman dinamico, apenas com taman baseado no numero de itens passados
	fmt.Println(array3)
	// array3[4] = 3 não
	fmt.Println("____________________________")
	slice := []int{1, 2, 3, 4, 5, 6}
	fmt.Println(slice)
	fmt.Println(reflect.TypeOf(slice))
	fmt.Println(reflect.TypeOf(array3))
	slice = append(slice, 12) //adiciona um item a mais no slice
	fmt.Println(slice)

	fmt.Println("__________Arrays Internos_______________")
	slice3 := make([]float32, 10, 11)
	fmt.Println(slice3)
	fmt.Println("tamanho: ", len(slice3))
	fmt.Println("capacidade: ", cap(slice3))
	slice3 = append(slice3, 12)
	fmt.Println(slice3)
	fmt.Println("tamanho: ", len(slice3))
	fmt.Println("capacidade: ", cap(slice3))

	slice3 = append(slice3, 13)
	fmt.Println(slice3)
	fmt.Println("tamanho: ", len(slice3))
	fmt.Println("capacidade: ", cap(slice3))

}
