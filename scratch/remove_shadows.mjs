import fs from 'fs';
import path from 'path';

const roots = [
  'resources/js',
  'resources/css',
  'resources/views'
];

function getAllFiles(dir, exts = ['.vue', '.css', '.blade.php']) {
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

const allFiles = roots.flatMap(r => {
  if (fs.existsSync(r)) return getAllFiles(r);
  return [];
});

console.log(`Found ${allFiles.length} files to scan.`);

let modifiedCount = 0;

for (const filePath of allFiles) {
  let content = fs.readFileSync(filePath, 'utf8');
  let original = content;

  // 1. Remove box-shadow properties or replace with none / border where appropriate
  // Replace CSS variable definitions
  content = content.replace(/--shadow-sm:\s*[^;]+;/g, '--shadow-sm: none;');
  content = content.replace(/--shadow-md:\s*[^;]+;/g, '--shadow-md: none;');
  content = content.replace(/--shadow-lg:\s*[^;]+;/g, '--shadow-lg: none;');
  content = content.replace(/--shadow-xl:\s*[^;]+;/g, '--shadow-xl: none;');
  content = content.replace(/--shadow-2xl:\s*[^;]+;/g, '--shadow-2xl: none;');

  // Remove box-shadow lines inside CSS rules
  // Example: box-shadow: 0 4px 12px rgba(...);
  // Example: box-shadow: var(--shadow-sm);
  // Example: box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.05);
  // Example: filter: drop-shadow(...);
  content = content.replace(/box-shadow:\s*[^;]+;\n?/g, '');
  content = content.replace(/text-shadow:\s*[^;]+;\n?/g, '');
  content = content.replace(/filter:\s*drop-shadow\([^)]+\);\n?/g, '');
  content = content.replace(/box-shadow\s*0\.[0-9]+s[^,]*,?/g, ''); // in transition list

  // In Tailwind classes or inline styles
  content = content.replace(/shadow-\[[^\]]+\]/g, '');
  content = content.replace(/\bshadow-sm\b/g, '');
  content = content.replace(/\bshadow-md\b/g, '');
  content = content.replace(/\bshadow-lg\b/g, '');
  content = content.replace(/\bshadow-xl\b/g, '');
  content = content.replace(/\bshadow-2xl\b/g, '');
  content = content.replace(/\bshadow\b/g, '');

  if (content !== original) {
    fs.writeFileSync(filePath, content, 'utf8');
    modifiedCount++;
    console.log(`Modified: ${filePath}`);
  }
}

console.log(`Finished! Modified ${modifiedCount} files.`);
