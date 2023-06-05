## Creating releases

Run

```
./tools/release
```

It will

- prompt for version number and changelog entry.
- creates a `release/v*` branch on the `public` remote
- the public remote will create a github release from the branch using a github workflow