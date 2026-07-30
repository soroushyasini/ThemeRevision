"use strict";

const fs = require("fs");
const path = require("path");

const pluginRoot = path.resolve(__dirname, "..");
const cssRoot = path.join(pluginRoot, "Asset", "dev", "css");
const cssFiles = [
    "basics.css",
    "form-components.css",
    "table.css",
    "layout.css",
    "login.css",
    "header.css",
    "plugins.css",
    "switcher-action-filter.css",
    "board.css",
    "task-detail.css",
    "project-overview.css",
    "sidebar.css",
    "table-list.css",
    "board-task-list.css",
    "activity-and-comment.css",
    "modal.css",
    "markdown.css",
    "other.css",
    "break-points.css",
    "modern.css"
];

const css = cssFiles
    .map((file) => fs.readFileSync(path.join(cssRoot, file), "utf8"))
    .join("\n")
    .replace(/\/\*[\s\S]*?\*\//g, "")
    .replace(/\s+/g, " ")
    .replace(/\s*([{};])\s*/g, "$1")
    .trim();

const openingBraces = (css.match(/{/g) || []).length;
const closingBraces = (css.match(/}/g) || []).length;
if (openingBraces !== closingBraces) {
    throw new Error(`CSS braces are unbalanced (${openingBraces} opening, ${closingBraces} closing).`);
}

fs.writeFileSync(path.join(pluginRoot, "Asset", "main.min.css"), `${css}\n`);
fs.copyFileSync(
    path.join(pluginRoot, "Asset", "dev", "js", "main.js"),
    path.join(pluginRoot, "Asset", "main.min.js")
);

console.log(`Built ${cssFiles.length} CSS sources and the JavaScript production asset.`);
