package random

import "math/rand"

const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"

func Bytes(n int) []byte {
	b := make([]byte, n)

	for i := range b {
		b[i] = chars[rand.Intn(len(chars))]
	}

	return b
}
