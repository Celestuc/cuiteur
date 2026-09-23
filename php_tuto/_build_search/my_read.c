#include <stdio.h>
#include <stdlib.h>

#define SIZE 150

int main(int argc, char*argv[]){
    if (argc != 3){
        fprintf(stderr, "%s FILE OFFSET\n", argv[0]);
        return 1;
    }
    FILE *f = fopen(argv[1], "r");
    if (f == NULL){
        perror("fopen");
        return 1;
    }
    fseek(f, atoi(argv[2]), SEEK_SET);
    char t[SIZE];
    size_t n = fread(t, sizeof(char), SIZE, f);
    fwrite(t, sizeof(char) ,n, stdout);
    printf("\n");
    int r = fclose(f);
    if (r == EOF){
        perror("fclose");
        return 1;
    }
    return 0;
}
