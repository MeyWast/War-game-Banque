# SOLUTIONS

## Vulnerabilities

### 1. Default Service Credentials

**Description:**
The application uses default credentials for certain services, such as the user `admin` with the password `password123`. These default credentials are often well-known and can be easily guessed by attackers.

**Functionality:**
During authentication, if an attacker uses the default credentials, they can access administrative or sensitive features of the application.

**Exploitation:**
An attacker can simply try the default credentials to access the application. For example, using `admin` and `password123` on the login page.

### 2. Path Traversal / Local File Inclusion (LFI)

**Description:**
The application allows users to download files without properly verifying the requested file path. This can allow an attacker to read sensitive files on the server.

**Functionality:**
By manipulating the request parameters, an attacker can access files outside the intended directory, such as `/etc/passwd`.

**Exploitation:**
An attacker can send a request with a malicious file path, for example `../../etc/passwd`, to read the contents of the `/etc/passwd` file.

### 3. XML External Entity (XXE)

**Description:**
The application can process XML inputs without disabling external entities, allowing an attacker to include malicious external entities in XML documents.

**Functionality:**
An attacker can inject external entities into an XML document to read local files or perform network requests from the server.

**Exploitation:**
An attacker can send an XML document containing an external entity, for example `<!ENTITY xxe SYSTEM "file:///etc/passwd">`, to read the contents of the `/etc/passwd` file.
