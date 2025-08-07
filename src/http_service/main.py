"""Application entry point for the HTTP service."""

from fastapi import FastAPI

app = FastAPI()


@app.get("/")
def read_root() -> dict[str, str]:
    """Return a friendly welcome message."""
    return {"message": "Hello, World!"}
