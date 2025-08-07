# http_service

Simple FastAPI-based HTTP service.

## Development

Install dependencies with:

```bash
pip install -e .[test]
```

Run the tests:

```bash
pytest
```

Start the development server:

```bash
uvicorn http_service.main:app --reload
```
