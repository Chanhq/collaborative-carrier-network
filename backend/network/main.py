from typing import Optional

import typer

app = typer.Typer()


@app.command()
def optimalpath(transportrequests: Optional[str] = None):
    typer.echo(transportrequests)


if __name__ == "__main__":
    app()
