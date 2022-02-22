CREATE VIEW `AgregateView` AS SELECT DISTINCT
	Forums.id_employee,
	Forums.id_meeting,
	Employees.fio,
	Employees.department,
	Meetings.starts_at,
	Meetings.ends_at,
	Meetings.`name`
FROM
	Employees
	INNER JOIN
	Forums
	ON
		Employees.id = Forums.id_employee
	INNER JOIN
	Meetings
	ON
		Forums.id_meeting = Meetings.id;