package main

import (
	"fmt"
	"time"
)

func main() {
	const (
		layoutISO = "2006-01-02"
		layoutUS  = "January 2, 2006"
	)
	date := "1999-12-31"
	t, _ := time.Parse(layoutISO, date)
	fmt.Println(t)                  // 1999-12-31 00:00:00 +0000 UTC
	fmt.Println(t.Format(layoutUS)) // December 31, 1999
}
