import sys
import typer
import json

from typing import Optional


from graphml_parser import GraphMLParser

sys.path.append("../")

app = typer.Typer()

@app.command()
def optimalpath(transportrequests: Optional[str] = None):
    transportrequests = json.loads(transportrequests)

    parser = GraphMLParser()
    g = parser.parse('../maps/default.graphml')



if __name__ == "__main__":
    app()
