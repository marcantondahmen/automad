jest.mock('nanoid', () => {
	return { nanoid: () => 'xxxx' };
});
