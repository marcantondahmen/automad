#!/bin/bash

. ./.env

# https://modelcontextprotocol.io/docs/2026-07-28/tools/inspector/web
export MCP_INSPECTOR_API_TOKEN=$AM_MCP_INSPECTOR_TOKEN

npx @modelcontextprotocol/inspector --web \
	--transport http \
	--server-url http://localhost:8000/mcp \
	--header "Authorization: Bearer $AM_MCP_SERVER_TOKEN"
