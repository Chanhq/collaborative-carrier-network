import json


def to_pickups_deliveries(transportrequests: str) -> [[int, int]]:
    result = []
    transportrequests = json.loads(transportrequests)

    for transportrequest in transportrequests:
        result.append([transportrequest.get('origin_node'), transportrequest.get('destination_node')])

    return result
