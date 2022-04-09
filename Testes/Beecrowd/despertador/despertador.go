package main

import (
	"fmt"
)

func main() {

	// var m1, h1, m2, h2 int
	// fmt.Scanf("%d %d %d %d", &h1, &m1, &h2, &m2)
	var sum int32

	for {

		fmt.Scanf("%d", &sum)

		if sum <= 0 {
			break
		} else {
			fmt.Println("Valor: ", sum)
		}

	}

	fmt.Println("ok")

}
