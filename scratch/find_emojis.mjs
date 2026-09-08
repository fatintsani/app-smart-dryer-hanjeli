import fs from 'fs';
import path from 'path';

const roots = ['resources/js', 'resources/views', 'resources/css'];

function getAllFiles(dir, exts = ['.vue', '.blade.php', '.js', '.css']) {
  let files = [];
  const entries = fs.readdirSync(dir, { withFileTypes: true });
  for (const entry of entries) {
    const fullPath = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      files = files.concat(getAllFiles(fullPath, exts));
    } else if (exts.some(ext => entry.name.endsWith(ext))) {
      files.push(fullPath);
    }
  }
  return files;
}

const emojiRegex = /[\u{1F300}-\u{1F9FF}]|[\u{2600}-\u{26FF}]|[\u{2700}-\u{27BF}]|[\u{1F1E6}-\u{1F1FF}]|[\u{1F600}-\u{1F64F}]|[\u{1F680}-\u{1F6FF}]|[\u{1F900}-\u{1F9FF}]|[\u{1FA00}-\u{1FA6F}]|[\u{1FA70}-\u{1FAFF}]|[\u{2300}-\u{23FF}]|[\u{2B50}]|[\u{200D}]|[\u{FE0F}]/gu;

const allFiles = roots.flatMap(r => fs.existsSync(r) ? getAllFiles(r) : []);

let found = [];

for (const filePath of allFiles) {
  const content = fs.readFileSync(filePath, 'utf8');
  const lines = content.split('\n');
  lines.forEach((line, idx) => {
    const matches = line.match(emojiRegex);
    if (matches && matches.length > 0) {
      found.push({
        file: filePath,
        line: idx + 1,
        emojis: matches.join(' '),
        content: line.trim()
      });
    }
  });
}

console.log(`Found ${found.length} lines with emojis:`);
found.forEach(f => {
  console.log(`${f.file}:${f.line} [${f.emojis}] -> ${f.content}`);
});
