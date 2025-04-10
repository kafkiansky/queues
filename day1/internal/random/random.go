package random

import "math/rand/v2"

const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"

//nolint:gosec
func Bytes(n int) []byte {
	b := make([]byte, n)

	for i := range b {
		b[i] = chars[rand.IntN(len(chars))]
	}

	return b
}
