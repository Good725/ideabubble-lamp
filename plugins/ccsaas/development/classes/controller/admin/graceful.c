#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>

/*
make the final executable with suid bit and try to run via php
not successful
*/

int main(int argc, char **argv)
{
    char *apachectl = "/usr/sbin/apachectl";
    char *params[] = {"/usr/sbin/apachectl", "graceful", NULL};

    execvp(apachectl, params);

    return 0;
}

