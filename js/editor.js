// Very small Markdown-to-HTML converter used ONLY for the live preview
// in the browser. The real, authoritative rendering happens server-side
// in view_blog.php using Parsedown (with safe mode on) — this client-side
// version just gives the writer a quick visual preview while typing.
function simpleMarkdownToHtml(md) {
    let html = md
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");

    // Headings
    html = html.replace(/^###### (.*)$/gim, "<h6>$1</h6>");
    html = html.replace(/^##### (.*)$/gim, "<h5>$1</h5>");
    html = html.replace(/^#### (.*)$/gim, "<h4>$1</h4>");
    html = html.replace(/^### (.*)$/gim, "<h3>$1</h3>");
    html = html.replace(/^## (.*)$/gim, "<h2>$1</h2>");
    html = html.replace(/^# (.*)$/gim, "<h1>$1</h1>");

    // Bold / italic
    html = html.replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>");
    html = html.replace(/\*(.*?)\*/g, "<em>$1</em>");

    // Inline code
    html = html.replace(/`([^`]+)`/g, "<code>$1</code>");

    // Blockquotes
    html = html.replace(/^> (.*)$/gim, "<blockquote>$1</blockquote>");

    // Links [text](url)
    html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" rel="noopener">$1</a>');

    // Paragraphs: wrap lines that aren't already block elements
    html = html
        .split(/\n{2,}/)
        .map(function (block) {
            const trimmed = block.trim();
            if (trimmed === "") return "";
            if (/^<h[1-6]>|^<blockquote>/.test(trimmed)) return trimmed;
            return "<p>" + trimmed.replace(/\n/g, "<br>") + "</p>";
        })
        .join("\n");

    return html;
}

document.addEventListener("DOMContentLoaded", function () {
    const textarea = document.getElementById("content");
    const preview = document.getElementById("preview");

    if (!textarea || !preview) return;

    function updatePreview() {
        const value = textarea.value.trim();
        preview.innerHTML = value
            ? simpleMarkdownToHtml(value)
            : '<p style="color:#aaa;">Your preview will appear here as you type...</p>';
    }

    textarea.addEventListener("input", updatePreview);
    updatePreview(); // initial render (e.g. when editing an existing post)
});
