import sys
import typer
import json

from typing import Optional
from pygraphml import GraphMLParser

sys.path.append("../")

app = typer.Typer()

@app.command()
def optimalpath(transportrequests: Optional[str] = None):
    transportrequests = json.loads(transportrequests)
    typer.echo(transportrequests)

    parser = GraphMLParser()
    g = parser.parse('../maps/default.graphml')

    g.show()

if __name__ == "__main__":
    app()
