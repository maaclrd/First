const validationKeyHints = {
    integer: 'Use um número inteiro neste campo.',
    numeric: 'Use um valor numérico neste campo.',
    required: 'Preencha este campo.',
    string: 'O texto informado é inválido.',
    min: 'O valor informado não é permitido.',
    gt: 'O valor deve ser maior que zero.',
    max: 'O valor excede o limite permitido.',
    unique: 'Este valor já está em uso.',
    email: 'Informe um e-mail válido.',
};

export function humanizeClientMessage(msg) {
    if (typeof msg !== 'string') {
        return msg;
    }
    const s = msg.trim();
    if (s.startsWith('validation.')) {
        const key = s.slice('validation.'.length);
        return validationKeyHints[key] ?? 'Verifique os dados informados.';
    }
    return s;
}

export function normalizeErrorBag(errors) {
    if (!errors || typeof errors !== 'object' || Array.isArray(errors)) {
        return errors;
    }
    const out = {};
    for (const [field, messages] of Object.entries(errors)) {
        const list = Array.isArray(messages) ? messages : [messages];
        out[field] = list.map((m) => humanizeClientMessage(m));
    }
    return out;
}
