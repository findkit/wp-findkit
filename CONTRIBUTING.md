## Javascript build

For Gutenberg blocks etc.

Install npm deps

```
npm ci
```

Start dev watcher

```
npm run dev
```

Production build

```
npm run build
```

No need to commit the production build. It is automatically create uppon release.

## Creating releases

Run

```
./tools/release
```

It will

- prompt for version number and changelog entry.
- creates a `release/v*` branch on the `public` remote
- the public remote will create a github release from the branch using a github workflow
