from typing import Optional

import typer
import json

app = typer.Typer()


@app.command()
def optimalpath(transportrequests: Optional[str] = None):
    transportrequests = json.loads(transportrequests)
    typer.echo(transportrequests[0]['id'])


if __name__ == "__main__":
    app()
