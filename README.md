BACKGROUND
==========
- This is a simple read-only tool to examine content in the NPR API.
- You can toggle between production and stage, filter your queries, or jump to a particular story (provided you know the ID).
- DO NOT USE on a publicly available site (as it exposes permissioned content). In fact, I'd only recommend using it on a local server. It is some quick and dirty PHP, let me tell you.

FAQ
===

**Q:** Why is this thing in PHP?

**A:** http://www.commitstrip.com/en/2015/01/12/the-right-tool-for-the-right-job/

**Q:** Why should I use this particular NPR API tool?

**A:** There are lots of great tools out there. I built nprbrowser because it was something I could easily run locally and tweak. More important, it is geared toward developers. Thus, the primary emphasis is on pouring out complicated NPR API docs into an easily viewable and traversable format (via the invaluable krumo). Prettiness, alas, is sacrificed.

INSTALLATION
============
- This assumes you already have a PHP + some sort of server (try `php -S localhost:8888`) on your local box.
- Download the code

```
git clone this repo
```

- Get krumo

```
cd pmpbrowser
git clone git@github.com:oodle/krumo.git
```

- Create a key file

```
cp key.sample.php key.php
```

- Edit key.php, replacing `<redacted>` with your credentials
- Edit your hosts file and server config files accordingly, so you can pull up nprbrowser in your (web) browser 
- Browse the NPR API!


